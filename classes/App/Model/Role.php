<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 15:14
 */


namespace App\Model;
use App\Events\PreRemoveEntityEvent;

/**
 * Role class for access control.
 * @package App\Model
 * @property string $name
 * @property boolean $removable
 */
class Role extends BaseModel
{
    public $table = 'tbl_roles';

    public static function roleRemoveListener(PreRemoveEntityEvent $event)
    {
        /** @var Role $entity */
        $entity = $event->getEntity();
        if ($entity->model_name == 'role' && $entity->loaded() && !$entity->removable) {
            $event->setCanRemove(false);
            $event->setReason('This role is not removable.');
        }
    }
} 