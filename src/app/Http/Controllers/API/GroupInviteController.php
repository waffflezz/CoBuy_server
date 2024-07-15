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
        $group = Group::find($groupId);
        if (!$group) {
            throw new ModelNotFoundException('Group not found');
        }

        if (is_null($group->invite_link) || !JWT::parse($group->invite_link)->isValid('inviteToken')) {
            $token = JWT::get('inviteToken', [
                'groupId' => $groupId,
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
            $token = JWT::parse($queryToken)->validate('inviteToken');
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Invalid token');
        }

        $groupId = $token->getPayload()['groupId'];

        $group = Group::find($groupId);
        if (!$group) {
            throw new ModelNotFoundException('Group with ID: ' . $groupId . ' not found');
        }

        if ($group->users()->where('users.id', Auth::id())->exists()) {
            throw new BadRequestHttpException('User already invited');
        }

        $group->users()->syncWithoutDetaching([Auth::user()->id]);

        return response()->json(['message' => 'User add to group!']);

    }
}
