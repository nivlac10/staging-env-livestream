<?php

namespace App\Console\Commands;
use App\Models\Livestream;
use App\Models\URL;
use Carbon\Carbon;
use Illuminate\Console\Command;

class autoUpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:streamStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan command to update stream status based on the time and url';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
	    $today = Carbon::now();
	    $currentDate = $today->format('Y-m-d');
	    $currentTime = $today->format('H:i');
        $hour = date('H:i');
	    $checkURL = [];
	    //Check if there is any stream links with URL then get their UID
	    $checkURL = URL::select('uid')->where('url','!=',null)->get();
        //illiterate the data obtained to pass over to query 
	    foreach ($checkURL as $u) {
	    	$uid[] = $u['uid'];
	    }
	    foreach ($uid as $t) {
	    	$notStarted = Livestream::where('start_date','=',$currentDate)->where('status','=','notStarted')->where('uid','=',$t)->update(['status' => 'inPlay']);
	    }
        //Update the livestream status to ended when there is no URL pass 15 minutes.
        $timeN = "00:16:00";
        $convertedN = date('H:i',strtotime($timeN));
        if($hour > $convertedN) {
            $noURL = Livestream::where('start_date','=',$currentDate)->where('time','<',Carbon::now()->addMinutes(-15))->where('status','=','notStarted')->update(['status' => 'ended']);
        } 
    }
}
