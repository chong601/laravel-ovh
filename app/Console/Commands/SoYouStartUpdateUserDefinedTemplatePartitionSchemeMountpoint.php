<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartUpdateUserDefinedTemplatePartitionSchemeMountpoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:installationtemplate:partitionscheme:mountpoint:update
                            {user_template_name : Name of the user template to update}
                            {partition_scheme_name : Name of the new partition scheme name}
                            {mountpoint : Name of the mountpoint to update}
                            {--filesystem= : Type of the filesystem}
                            {--updatedMountpoint= : Mount point for the partition}
                            {--order= : Which number of the partition to be placed at (defaults to end of the partition if not defined)}
                            {--raid= : RAID level for the partition}
                            {--size= : Size of the partition in MiB (defaults to use all space if not defined)}
                            {--type= : One of the the following: primary, logical or lv}
                            {--volumeName= : Volume name to be created (only applicable for type lv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a mountpoint that is part of the partition scheme for the installation tenplate';

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
        $mountpoint = $this->argument('mountpoint');

        // Optional data
        $filesystem = $this->option('filesystem');
        $updatedMountpoint = $this->option('updatedMountpoint');
        $order = $this->option('order');
        $raid = $this->option('raid');
        $size = $this->option('size');
        $type = $this->option('type');
        if($type === 'lv') {
            $volumeName = $this->option('volumeName');
        } else {
            $volumeName = null;
        }

        if ($mountpoint !== 'swap' && $mountpoint[0] !== '/') {
            $this->error('Mountpoint must start with a / except for "swap"!');
            return 1;
        }

        // Check user template exists
        if (!in_array($userTemplateName, $ovh_api->getAllUserDefinedInstallationTemplates())) {
            $this->error(sprintf('User template "%s" not found!', $userTemplateName));
            return 1;
        }

        // Check partition scheme exists
        if (!in_array($partitionSchemeName, $ovh_api->getUserDefinedInstallationTemplatePartitionSchemes($userTemplateName))) {
            $this->error(sprintf('Partition scheme "%s" not found!', $partitionSchemeName));
            return 1;
        }
        $mountPoints = $ovh_api->getUserDefinedInstallationTemplatePartitionMountpoints($userTemplateName, $partitionSchemeName);
        // Check if mount point exists
        if (!in_array($mountpoint, $mountPoints)) {
            $this->error(sprintf('Mount point "%s" not found!', $mountpoint));
            return 1;
        }

        $finalSize = [
            'unit' => 'MB',
            'value' => $size
        ];

        // Get supported filesystems
        // We would like to lock out filesystems that is not supported by this template
        $supportedFilesystems = $ovh_api->getUserDefinedInstallationTemplateDetails($userTemplateName);

        $this->info(json_encode([
            'arguments' => [
                'userTemplateName' => $userTemplateName,
                'partitionSchemeName' => $partitionSchemeName,
                'mountpoint' => $mountpoint,
            ],
            'options' => [
                'filesystem' => $filesystem,
                'updatedMountpoint' => $updatedMountpoint,
                'order' => $order,
                'raid' => $raid,
                'size' => $finalSize,
                'type' => $type,
                'volumeName' => $volumeName
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info(json_encode([
            'templateName' => $userTemplateName,
            'schemeName' => $partitionSchemeName,
            'mountpoint' => $mountpoint,
            'templatePartitions' => [
                'filesystem' => $filesystem,
                'updatedMountpoint' => $updatedMountpoint,
                'order' => $order,
                'raid' => $raid,
                'size' => $finalSize,
                'type' => $type,
                'volumeName' => $volumeName
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // try {
        //     $ovh_api->putUpdateUserDefinedTemplatePartitionSchemeMountpoint(
        //         $userTemplateName,
        //         $partitionSchemeName,
        //         $filesystem,
        //         $mountpoint,
        //         $raid,
        //         $size,
        //         $order,
        //         $type,
        //         $volumeName
        //     );

        //     $this->info(sprintf('Mountpoint "%s" created successfully!', $mountpoint));
        // } catch (Exception $e) {
        //     $this->error($e->getMessage());
        //     return 1;
        // }
        return 0;
    }
}
