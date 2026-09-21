<?php 

use App\Http\Requests\StoreTodoRequest;

readonly class CreateTodoData 
{
    public function __construct(
        public string $title,
        public int $userId
    ){}

    public static function fromRequest(
        StoreTodoRequest $request
    ): self {
        return new self(
            title: $request->string('title')->toString(),
            userId: $request->user()->id
        );
    }
}


