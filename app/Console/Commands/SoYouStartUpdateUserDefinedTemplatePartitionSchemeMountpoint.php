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
        $volumeName = $this->option('volumeName');

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
        if (!in_array($mountpoint, $mountPoints)) {
            $this->error(sprintf('Mount point "%s" not found!', $mountpoint));
            return 1;
        }

        // Bail out if the updated filesystem matches any existing filesystems
        if (is_string($updatedMountpoint) && in_array($updatedMountpoint, $mountPoints)) {
            $this->error(sprintf('Cannot update mountpoint "%s" to already existing mountpoint "%s"!', $mountpoint, $updatedMountpoint));
            return 1;
        }

        // Get supported filesystems
        // We would like to lock out filesystems that is not supported by this template
        if (is_string($filesystem)) {
            $supportedFilesystems = $ovh_api->me->installationTemplate->get($userTemplateName);
            if (!in_array($filesystem, $supportedFilesystems['filesystems'])) {
                $this->error(sprintf('Filesystem "%s" is not valid!, Valid filesystems are %s.', $filesystem, implode(', ', $supportedFilesystems['filesystems'])));
                return 1;
            }
        }

        // Assemble the updated data
        $templatePartitions = [];

        if (isset($filesystem)) {
            $templatePartitions['filesystem'] = $filesystem;
        }

        if (isset($updatedMountpoint)) {
            $templatePartitions['mountpoint'] = $updatedMountpoint;
        }

        if (isset($order) && is_numeric($order)) {
            $templatePartitions['order'] = $order;
        }

        if (isset($raid) && is_numeric($raid)) {
            $templatePartitions['raid'] = intval($raid);
        }

        if (isset($size) && is_numeric($size)) {
            $finalSize = [
                'unit' => 'MB',
                'value' => intval($size)
            ];

            $templatePartitions['size'] = $finalSize;
        }

        if (isset($type)) {
            $templatePartitions['type'] = $type;
        }

        if (isset($volumeName)) {
            $templatePartitions['volumeName'] = $volumeName;
        }

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
            'templatePartitions' => $templatePartitions
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if (empty($templatePartitions)) {
            $this->error('Nothing to update!');
            return 1;
        }

        try {
            $ovh_api->me->installationTemplate->partitionScheme->partition->update(
                $userTemplateName,
                $partitionSchemeName,
                $mountpoint,
                $templatePartitions
            );

            $this->info(sprintf('Mountpoint "%s" updated successfully!', $mountpoint));
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
        return 0;
    }
}
