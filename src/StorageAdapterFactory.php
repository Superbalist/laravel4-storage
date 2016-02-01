<?php
namespace Superbalist\Storage;

use Aws\S3\S3Client;
use Config;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Rackspace\RackspaceAdapter;
use OpenCloud\Rackspace;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use Superbalist\Storage\Adapter\Local;

abstract class StorageAdapterFactory
{

    /**
     * @param string $name
     * @return \League\Flysystem\AdapterInterface
     * @throws \RuntimeException
     */
    public static function make($name)
    {
        $connections = Config::get('storage.connections');

        if (!isset($connections[$name])) {
            throw new \RuntimeException(sprintf('The storage connection %d does not exist.', $name));
        }

        $connection = $connections[$name];
        $connection['adapter'] = strtoupper($connection['adapter']);

        switch ($connection['adapter']) {
            case 'LOCAL':
                return new Local($connection['root_path'], $connection['public_url_base']);
            case 'RACKSPACE':
                $service = isset($connection['service']) ? Config::get($connection['service']) : Config::get(
                    'services.rackspace'
                );
                $client = new Rackspace(
                    $service['api_endpoint'],
                    array(
                        'username' => $service['username'],
                        'tenantName' => $service['tenant_name'],
                        'apiKey' => $service['api_key']
                    )
                );
                $store = $client->objectStoreService(
                    $connection['store'],
                    $connection['region']
                );
                $container = $store->getContainer($connection['container']);
                return new RackspaceAdapter($container);
            case 'AWS':
                $service = isset($connection['service']) ? Config::get($connection['service']) : Config::get(
                    'services.aws'
                );
                $client = S3Client::factory(
                    array(
                        'credentials' => array(
                            'key' => $service['access_key'],
                            'secret' => $service['secret_key'],
                        ),
                        'region' => $service['region'],
                        'version' => 'latest'
                    )
                );
                return new AwsS3Adapter($client, $connection['bucket']);
            case 'GCLOUD':
                $service = isset($connection['service']) ? Config::get($connection['service']) : Config::get(
                    'services.google_cloud'
                );
                $credentials = new \Google_Auth_AssertionCredentials(
                    $service['service_account'],
                    [\Google_Service_Storage::DEVSTORAGE_FULL_CONTROL],
                    file_get_contents($service['key_file']),
                    $service['secret']
                );

                $config = new \Google_Config();
                $config->setAuthClass(GoogleAuthOAuth2::class);
                $client = new \Google_Client($config);
                $client->setAssertionCredentials($credentials);
                $client->setDeveloperKey($service['developer_key']);

                $service = new \Google_Service_Storage($client);

                return new GoogleStorageAdapter($service, $connection['bucket']);

        }
        throw new \RuntimeException(sprintf('The storage adapter %s is invalid.', $connection['adapter']));
    }

    /**
     * @param string $name
     * @return \League\Flysystem\AdapterInterface
     */
    public static function makeCached($name)
    {
        $adapter = self::make($name);
        $store = new Memory();
        return new CachedAdapter($adapter, $store);
    }
}