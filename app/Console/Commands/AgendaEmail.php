<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AgendaEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:agenda-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agenda email ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $debug = true;

    	$users = User::where([
            'domain_id' => 31, // mbda
            'role' => 5 // event user
        ])
        ->get();
    	echo count($users) . ' users...';
        foreach ($users as $user) {
            if (empty($user->email_verified_at)) {
                $user->resend_verify = true;
            }
            if ($debug) $user->email = 'peter@askuschat.com';
            try {
                $user->sendAgendaNotification();
            } catch (\Exception $e) {
                echo 'ERROR\n';
                print_r(['user' => $user->id, 'email' => $user->email, $e->getMessage()]);
                echo '\n';
            }
            echo "??" . $user->id . "\n";
            if ($debug) break;
            usleep(10);
        }

    }
}