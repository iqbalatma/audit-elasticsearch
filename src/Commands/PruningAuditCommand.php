<?php

namespace Iqbalatma\AuditElasticsearch\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruningAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:prune {--r= : Retention}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for prune data audit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Pruning data audit");
        $now = Carbon::now()->endOfDay();

        $optionRetention = $this->option("r");

        if($optionRetention !== null){
            if(ctype_digit($optionRetention)){
                $retention = $optionRetention;
            }else{
                $this->error("Retention option must be integer");
                return;
            }
        }else{
            $retention=config("app.audit_log_retention");
        }

        $targetDate = $now->subDays($retention);
        $deletedRow = DB::table("audits")->whereDate("created_at", "<=", $targetDate)->delete();
        $this->info("Pruning data audit successfully. Total $deletedRow deleted");
    }
}
