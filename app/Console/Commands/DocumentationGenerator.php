<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DocumentationGenerator extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doc:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Fortis Aggregator API Documentation';

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
        $destination = base_path('docs/aggregator-v1.html');
        $source = base_path('docs/aggregator-v1.md');
        $command = "aglio -i $source --theme-full-width -t default -o $destination";
        shell_exec($command);

    }

}