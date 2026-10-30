<?php

namespace App\Services;

use App\Models\Code;
use App\DTOs\CodeCreateDTO;


class CodeService
{
    public function getByType(string $type)
    {
        $codes = Code::where('type', $type)->get();

        return $codes;
    }

    public function getAll()
    {
        $codes = Code::all();

        return $codes;
    }

    public function store(CodeCreateDTO $data)
    {
        $code = Code::create([
            'name' => $data->name,
            'type' => $data->type,
            'description' => $data->description
        ]);

        return [
            'success' => true
        ];
    }
}
