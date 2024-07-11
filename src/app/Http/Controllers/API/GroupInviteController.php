<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use STS\JWT\Facades\JWT;

class GroupInviteController extends Controller
{
    public function getInviteLink(string $groupId)
    {
        $group = Group::findOrFail($groupId);

        if (is_null($group->invite_link) || !JWT::parse($group->invite_link)->isValid('invite_token')) {
            $token = JWT::get('invite_token', [
                'group_id' => $groupId,
            ]);
            $group->invite_link = $token;
            $group->save();
            return $token;
        }

        return $group->invite_link;
    }

    public function invite(Request $request)
    {
        $queryToken = $request->query('token', 'null');
        try {
            $token = JWT::parse($queryToken)->validate('invite_token');

            $group_id = $token->getPayload()['group_id'];
            $group = Group::findOrFail($group_id);

            $group->users()->syncWithoutDetaching([Auth::user()->id]);

            return response()->json(['message' => 'User add to group!']);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
