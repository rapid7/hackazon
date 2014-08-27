# IGNORE

DROP FUNCTION IF EXISTS `rebuild_nested_set_tree`;

# CREATE FUNCTION `rebuild_nested_set_tree`() RETURNS INT(11)
# DETERMINISTIC MODIFIES SQL DATA
# BEGIN
#     UPDATE `tbl_categories` SET lpos = NULL, rpos = NULL, depth = 0;
#
#     SET @i := 0;
#     UPDATE `tbl_categories` t SET lpos = (@i := @i + 1), rpos = (@i := @i + 1), depth = 0
#     WHERE t.parent IS NULL;
#
#     forever: LOOP
# 				SET @parent_id := NULL;
# 				SET @parent_level := NULL;
#
#         SELECT t.CategoryID, t.rpos, t.depth FROM `tbl_categories` t, `tbl_categories` tc
#         WHERE t.CategoryID = tc.parent AND tc.lpos IS NULL AND t.rpos IS NOT NULL
#         ORDER BY t.rpos LIMIT 1 INTO @parent_id, @parent_right, @parent_level;
#
#         IF @parent_id IS NULL THEN LEAVE forever; END IF;
#
#         SET @current_left := @parent_right;
#
#         SELECT @current_left + COUNT(*) * 2 FROM `tbl_categories`
#         WHERE parent = @parent_id INTO @parent_right;
#
# 		UPDATE `tbl_categories` t SET depth = @parent_level+1 WHERE parent = @parent_id;
#
#         SET @current_length := @parent_right - @current_left;
#
#         UPDATE `tbl_categories` t SET rpos = rpos + @current_length
#         WHERE rpos >= @current_left ORDER BY rpos;
#
#         UPDATE `tbl_categories` t SET lpos = lpos + @current_length
#         WHERE lpos > @current_left ORDER BY lpos;
#
#         SET @i := (@current_left - 1);
#         UPDATE `tbl_categories` t SET lpos = (@i := @i + 1), rpos = (@i := @i + 1)
#         WHERE parent = @parent_id ORDER BY CategoryID;
#     END LOOP;
#
#     RETURN (SELECT MAX(rpos) FROM `tbl_categories` t);
# END