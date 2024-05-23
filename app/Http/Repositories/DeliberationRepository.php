<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\DeliberationInterface;
use App\Http\Requests\Deliberation\CreateDeliberationRequest;
use App\Models\Defence;
use App\Models\MemberDeliberation;
use App\Models\Student;
use App\Notifications\DeliberationNotification;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Diff\Exception;

class DeliberationRepository implements DeliberationInterface
{
    public function __construct(
        private Defence $defence,
        private MemberDeliberation $deliberation,
    ){}  
    

    public function create(CreateDeliberationRequest $request, $defence_id): array 
    {
        $defence = $this->defence::find($request->defence_id);
        if(!$defence){
            return [
                'success' => false,
                'message' =>'defence not found'
            ];
        }
        if(!$defence->jurys()->where([
            'jury_id' => auth()->id(),
            'role' => $this->defence::JURY_ROLES['PRESIDENT']
        ])->exists()){
            return [
                'success' => false,
                'message' => 'you are not the jury president'
            ];
        }
        $memberIds = $defence->project->members->pluck('id')->toArray();
        $ids = array_column($request->data, 'member_id');
        if (count($ids) === count($memberIds) && !empty(array_diff($memberIds, $ids))) {
            return [
                'success' => false,
                'message' => 'please provide all members'
            ];
        }
        if (count($defence->deliberations) > 0) {
            return [
                'success' => false,
                'message' => 'deliberation already added',
            ];
        }
        try{
            DB::transaction(function() use ($request, &$defence){
                foreach ($request->data as $member) {
                    $this->deliberation::create([
                        'diploma_url' => '',
                        'mark' => $member['mark'],
                        'mention' => $member['mention'],
                        'appreciation' => $member['appreciation'],
                        'member_id' => $member['member_id'],
                        'defence_id' => $defence->id,
                    ]);
                }
                $defence->reserves = $request->reserves ?? null;
                $defence->save();
                $defence->refresh();
            });

            foreach ($defence->jurys as $jury) {
                $jury->notify(new DeliberationNotification($defence, Teacher::class));
            }

            foreach ($defence->project->members as $member) {
                $member->notify(new DeliberationNotification($defence, Student::class));
            }

            return [
                'success' => true,
                'message' => 'deliberation added successfully',
                'deliberation' => $defence
            ];
        }
            catch(Exception $e){
                return [
                    'success' => false,
                    'message' => "something went wrong. please try again"
                ];
        }
    }

    public function getByDefence($defence_id): array
    {
        $defence = $this->defence::find($defence_id);
        if(!$defence){
            return [
                'success' => false,
                'message' =>'defence not found'
            ];
        }
        $auth = auth()->user();
        if (!$defence->allow_view($auth)){
            return [
                'success' => false,
                'message' => 'not allowed to view this deliberation'
            ];
        }
        switch ($auth->person_type) {
            case Student::class:
                $deliberation = $defence->deliberations()->where('member_id', $auth->id)->get();
                break;
            
            default:
                $deliberation = $defence->deliberations;
                break;
        }
        if(count($deliberation) === 0){
            return [
                'success' => false,
                'message' => 'deliberation not yet added',
            ];
        }
        $defence->deliberations = $deliberation;
        return [
            'success' => true,
            'message' => 'deliberation retrieved',
            'deliberation' => $defence
        ];
        
    }
}