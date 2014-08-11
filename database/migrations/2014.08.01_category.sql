ALTER TABLE `tbl_categories` ADD `lpos` INT(11) default NULL;
ALTER TABLE `tbl_categories` ADD `rpos` INT(11) default NULL;
ALTER TABLE `tbl_categories` ADD `depth` INT(11) default NULL;

ALTER TABLE `tbl_review` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_categories` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_category_product` COMMENT='' ENGINE='InnoDB';

INSERT INTO `tbl_categories` (categoryID, name, parent) VALUES ('', 'ROOT', null);
UPDATE `tbl_categories` SET parent = LAST_INSERT_ID() WHERE parent=0;

DELIMITER //

DROP FUNCTION IF EXISTS rebuild_nested_set_tree//

CREATE FUNCTION rebuild_nested_set_tree()
RETURNS INT DETERMINISTIC MODIFIES SQL DATA
BEGIN
    -- Изначально сбрасываем все границы в NULL
    UPDATE `tbl_categories` SET lpos = NULL, rpos = NULL, depth = 0;
    
    -- Устанавливаем границы корневым элементам
    SET @i := 0;
    UPDATE `tbl_categories` t SET lpos = (@i := @i + 1), rpos = (@i := @i + 1), depth = 0
    WHERE t.parent IS NULL;

    forever: LOOP
				SET @parent_id := NULL;
				SET @parent_level := NULL;

        -- Находим элемент с минимальной правой границей -- самый левый в дереве
        SELECT t.CategoryID, t.rpos, t.depth FROM `tbl_categories` t, `tbl_categories` tc
        WHERE t.CategoryID = tc.parent AND tc.lpos IS NULL AND t.rpos IS NOT NULL
        ORDER BY t.rpos LIMIT 1 INTO @parent_id, @parent_right, @parent_level;

        -- Выходим из бесконечности, когда у нас уже нет незаполненных элементов
        IF @parent_id IS NULL THEN LEAVE forever; END IF;

        -- Сохраняем левую границу текущего ряда
        SET @current_left := @parent_right;

        -- Вычисляем максимальную правую границу текущего ряда
        SELECT @current_left + COUNT(*) * 2 FROM `tbl_categories`
        WHERE parent = @parent_id INTO @parent_right;

				-- Апдейт левела текущего ряда
				UPDATE `tbl_categories` t SET depth = @parent_level+1 WHERE parent = @parent_id;

        -- Вычисляем длину текущего ряда
        SET @current_length := @parent_right - @current_left;

        -- Обновляем правые границы всех элементов, которые правее
        UPDATE `tbl_categories` t SET rpos = rpos + @current_length
        WHERE rpos >= @current_left ORDER BY rpos;

        -- Обновляем левые границы всех элементов, которые правее
        UPDATE `tbl_categories` t SET lpos = lpos + @current_length
        WHERE lpos > @current_left ORDER BY lpos;

        -- И только сейчас обновляем границы текущего ряда
        SET @i := (@current_left - 1);
        UPDATE `tbl_categories` t SET lpos = (@i := @i + 1), rpos = (@i := @i + 1)
        WHERE parent = @parent_id ORDER BY CategoryID;
    END LOOP;

    -- Возвращаем самый самую правую границу для дальнейшего использования
    RETURN (SELECT MAX(rpos) FROM `tbl_categories` t);
END//
DELIMITER ;

INSERT INTO `tbl_category_product` (`productID`,`CategoryID`) SELECT t.ProductID, t.CategoryID FROM tbl_products as t;