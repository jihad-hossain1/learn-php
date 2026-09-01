<?php

interface TodoRepository 
{
    public function add(array $task): void;

    public function all(): array;

    public function remove(int $taskId): bool;
}

class InMemoryTodoRepository implements TodoRepository 
{
    private array $tasks = [];

    public function add(array $task): void 
    {
        $this->tasks[] = $task;
    }

    public function all(): array 
    {
        return $this->tasks;
    }

    public function remove(int $taskId): bool 
    {
        foreach ($this->tasks as $index => $task){
            if($task['id'] === $taskId){
                unset($this->tasks[$index]);

                $this->tasks = array_values($this->tasks);

                return true;
            }
        }

        return false;

    }
}


class TodoService 
{
    public function __construct(
        private TodoRepository $repo
    ) {}

    public function addTask(string $title): void 
    {
        $this->repo->add(
            [
                'id' => random_int(1,999999),
                'title' => $title ,
                'completed' => false
            ]
        );
    }
   

    public function getTasks(): array 
    {
        return $this->repo->all();
    }
}

$todo = new TodoService();
$todo->addTask('Learn Laravel.');
$todoList = $todo->getTasks();

print_r($todoList);


interface NotificationSender
{
    public function send(string $msg): void;
}

class EmailNotificationSender implements NotificationSender 
{
    public function send(string $msg): void 
    {
        echo 'email send done.' . PHP_EOL;
    }

}

class SMSNotificationSender implements NotificationSender 
{
    public function send(string $msg): void 
    {
        echo 'SMS send done.' . PHP_EOL;
    }

}

class NotificationService 
{
    public function __construct(
        private NotificationSender $sender
    ){}

    public function notify(string $msg): void 
    {
        $this->sender->send($msg);
    }
}


