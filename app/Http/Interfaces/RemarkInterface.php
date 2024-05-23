<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Remark\CreateRemarkRequest;
use App\Http\Requests\Remark\UpdateRemarkRequest;

interface RemarkInterface
{   
    public function create(CreateRemarkRequest $request) : array;
    public function destroy($id) : array;
    public function update(UpdateRemarkRequest $request) : array;
    public function get_by_projectId($id) : array;
}