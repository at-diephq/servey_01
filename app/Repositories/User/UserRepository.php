<?php

namespace  App\Repositories\User;

use DB;
use Exception;
use App\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function changeStatus($ids, $status)
    {
        DB::beginTransaction();
        try {
            $ids = (is_array($ids) ? $ids : [$ids]);
            $this->multiUpdate('id', $ids, ['status' => $status]);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public function uploadAvatar($file)
    {
        if (!$file) {
            return config('users.avatar_default');
        }

        $fileName = uniqid(rand(), true) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path(config('users.avatar_path')), $fileName);

        return $fileName;
    }
}
