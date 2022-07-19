<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartCreateNewUserTemplatePartitionSchemeMountpoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // $userTemplateName, $partitionSchemeName, $filesystem, $mountpoint, $raid, $size, $step, $type, $volumeName = null
    protected $signature = 'soyoustart:me:usertemplate:partitionscheme:mountpoint:create
    {user_template_name : Name of the user template} {partition_scheme_name : Name of the new partition scheme name}
    {filesystem : Type of the filesystem}
    {mountpoint : Mount point for the partition}
    {--raid=1 : RAID level for the partition}
    {--size=0 : Size of the partition in MiB (defaults to use all space if not defined)}
    {--step= : Which number of the partition to be placed at (defaults to end of the partition if not defined)}
    {--type=primary : One of the the following: primary, logical or lv}
    {--volumeName= : Volume name to be created (only applicable for type lv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new mountpoint on a user-defined template';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var SoYouStartService */
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        // Required data
        $userTemplateName = $this->argument('user_template_name');
        $partitionSchemeName = $this->argument('partition_scheme_name');
        $filesystem = $this->argument('filesystem');
        $mountpoint = $this->argument('mountpoint');

        // Optional data
        $type = $this->option('type');
        $raid = intval($this->option('raid')) ?? 1;
        $size = intval($this->option('size'));
        $step = intval($this->option('step'));
        if($type === 'lv') {
            $volumeName = $this->option('volumeName');
        } else {
            $volumeName = '';
        }

        if ($mountpoint !== 'swap' && $mountpoint[0] !== '/') {
            $this->error('Mountpoint must start with a / except for "swap"!');
            return 1;
        }

        // Check user template exists
        if (!in_array($userTemplateName, $ovh_api->me->installationTemplate->all())) {
            $this->error(sprintf('User template "%s" not found!', $userTemplateName));
            return 1;
        }

        // Check partition scheme exists
        if (!in_array($partitionSchemeName, $ovh_api->me->installationTemplate->partitionScheme->all($userTemplateName))) {
            $this->error(sprintf('Partition scheme "%s" not found!', $partitionSchemeName));
            return 1;
        }
        $mountPoints = $ovh_api->me->installationTemplate->partitionScheme->partition->all($userTemplateName, $partitionSchemeName);
        // Check if mount point exists
        if (in_array($mountpoint, $mountPoints)) {
            $this->error(sprintf('Mount point "%s" already exists!', $mountpoint));
            return 1;
        }

        // Get supported filesystems
        // We would like to lock out filesystems that is not supported by this template
        $supportedFilesystems = $ovh_api->me->installationTemplate->get($userTemplateName);

        if (!in_array($filesystem, $supportedFilesystems['filesystems'])) {
            $this->error(sprintf('Filesystem "%s" is not valid! Accepted filesystems: %s', $filesystem, implode(', ', $supportedFilesystems['filesystems'])));
            return 1;
        }

        if ($type === 'lv' && empty($volumeName)) {
            $this->error('Volume name is required when using type lv');
            return 1;
        }

        // Set step to array length if step is not defined
        if (empty($step)) {
            $step = count($mountPoints);
            $this->warn(sprintf('Step is not defined: using %u for step value!', $step));
        }

        $this->info(print_r([
            'userTemplateName' => $userTemplateName,
            'partitionSchemeName' => $partitionSchemeName,
            'filesystem' => $filesystem,
            'mountpoint' => $mountpoint,
            'raid' => $raid,
            'type' => $type,
            'size' => $size,
            'step' => $step,
            'volumeName' => $volumeName
        ], true));

        try {
            $ovh_api->me->installationTemplate->partitionScheme->partition->create(
                $userTemplateName,
                $partitionSchemeName,
                $filesystem,
                $mountpoint,
                $raid,
                $size,
                $step,
                $type,
                $volumeName
            );

            $this->info(sprintf('Mountpoint "%s" created successfully!', $mountpoint));
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
