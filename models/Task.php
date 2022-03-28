<?php
namespace app\models;

class Task
{
    const STATUS_NEW = 'new';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_IN_PROCESS = 'inProcess';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const ACTION_CANCEL = 'cancel';
    const ACTION_RESPOND = 'respond';
    const ACTION_COMPLETE = 'complete';
    const ACTION_REFUSE = 'refuse';

    private $customerId;
    private $executorId;
    private $userID;

    public function __construct($customerId, $executorId)
    {
        $this->customerId = $customerId;
        $this->executorId = $executorId;
    }

    // Возвращает "карту" доступных статусов
    public function getStatusesMap()
    {
        $statuses =
            [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCELLED => 'Отменено',
            self::STATUS_IN_PROCESS => 'В работе',
            self::STATUS_COMPLETED => 'Выполнено',
            self::STATUS_FAILED => 'Провалено',
        ];

        return $statuses;
    }

    // Возвращает "карту" доступных действий
    public function getActionsMap()
    {
        $actions = [
            self::ACTION_CANCEL => 'Отменить',
            self::ACTION_RESPOND => 'Откликнуться',
            self::ACTION_COMPLETE => 'Выполнено',
            self::ACTION_REFUSE => 'Отказаться',
        ];

        return $actions;
    }

    // Возвращает имя статуса, в который перейдёт задание после выполнения действия $action
    public function getNewStatus($action)
    {
        switch ($action) {
            case self::ACTION_CANCEL:
                return self::STATUS_CANCELLED;
                break;
            case self::ACTION_RESPOND:
                return self::STATUS_IN_PROCESS;
                break;
            case self::ACTION_COMPLETE:
                return self::STATUS_COMPLETED;
                break;
            case self::ACTION_REFUSE:
                return self::STATUS_FAILED;
                break;
            default:
                return false;
        }
    }

    // Определяет список доступных действий в текущем статусе
    public function getAvailableActions($status, $userID, $customerId, $executorId)
    {
        switch ($status) {
            case self::STATUS_NEW:
                $action = new ActionCancel;
                if ($action->checkUserRole($userID, $customerId, $executorId)) {
                    return $action->getActionName();
                } else {
                    $action = new ActionRespond;
                    return $action->getActionName();
                }
                break;
            case self::STATUS_CANCELLED:
                return false;
                break;
            case self::STATUS_IN_PROCESS:
                $action = new ActionComplete;
                if ($action->checkUserRole($userID, $customerId, $executorId)) {
                    return $action->getActionName();
                } else {
                    $action = new ActionRefuse;
                    return $action->getActionName();
                }
                break;
            case self::STATUS_COMPLETED:
                return false;
                break;
            case self::STATUS_FAILED:
                return false;
                break;
        }
    }

}
