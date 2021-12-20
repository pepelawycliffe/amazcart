<?php

namespace Modules\Customer\Repositories;
use Modules\Customer\Entities\CustomerAddress;
use App\Models\User;
use Modules\Setup\Entities\Country;
use Modules\Setup\Entities\State;

class CustomerRepository
{
    public function getAll()
    {
        return User::with('wallet_balances', 'orders')->where('role_id', 4);
    }

    public function find($id)
    {
        return User::with('wallet_balances', 'orders', 'customerAddresses')->findOrFail($id);
    }

}
