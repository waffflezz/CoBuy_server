<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use STS\JWT\Facades\JWT;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
            return response()->json(['token' => $token]);
        }

        return response()->json(['token' => $group->invite_link]);
    }

    public function invite(Request $request)
    {
        $queryToken = $request->query('token', 'null');
        try {
            $token = JWT::parse($queryToken)->validate('invite_token');
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Invalid token');
        }

        $group_id = $token->getPayload()['group_id'];

        $group = Group::find($group_id);
        if (!$group) {
            throw new ModelNotFoundException('Group with ID: ' . $group_id . ' not found');
        }

        if ($group->users()->contains(Auth::user())) {
            throw new BadRequestHttpException('User already invited');
        }

        $group->users()->syncWithoutDetaching([Auth::user()->id]);

        return response()->json(['message' => 'User add to group!']);

    }
}
