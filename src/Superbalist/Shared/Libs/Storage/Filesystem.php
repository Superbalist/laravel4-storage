<?php namespace Superbalist\Shared\Libs\Storage;

use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Filesystem as BaseFilesystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use Symfony\Component\Finder\Adapter\AdapterInterface;

class Filesystem extends BaseFilesystem {

	/**
	 * @param string $path
	 * @param int $ttl
	 * @return string|null
	 */
	public function getSignedUrl($path, $ttl = 7200)
	{
		// TODO: handle multiple adapters here
		$adapter = $this->getRealAdapter();
		if ($adapter instanceof GoogleStorageAdapter) {
			// see https://cloud.google.com/storage/docs/access-control?hl=en#Signed-URLs
			$expires = time() + $ttl;
			$bucket = trim($adapter->getBucket(), '/');
			$path = trim($path, '/');
			$raw = sprintf("GET\n\n\n%d\n/%s/%s", $expires, $bucket, $path);

			// we need to grab the auth class which contains the credentials
			// in order to get the credentials, we've had to extend google's Google_Auth_OAuth2 class to create a public
			// getAssertionCredentials() function
			$service = $adapter->getService();
			$client = $service->getClient();
			$auth = $client->getAuth(); /** @var GoogleAuthOAuth2 $auth */
			$credentials = $auth->getAssertionCredentials();

			$signer = new \Google_Signer_P12($credentials->privateKey, $credentials->privateKeyPassword);
			$signature = $signer->sign($raw);

			$params = array(
				'GoogleAccessId' => $credentials->serviceAccountName,
				'Expires' => $expires,
				'Signature' => base64_encode($signature)
			);
			return sprintf('https://storage.googleapis.com/%s/%s?%s', $bucket, $path, http_build_query($params));
		}

		return null;
	}

	/**
	 * @param string $path
	 * @return string|null
	 */
	public function getPublicUrl($path)
	{
		// TODO: handle multiple adapters here
		$adapter = $this->getRealAdapter();
		if ($adapter instanceof GoogleStorageAdapter) {
			$bucket = trim($adapter->getBucket(), '/');
			$path = trim($path, '/');
			return sprintf('https://storage.googleapis.com/%s/%s', $bucket, $path);
		}

		return null;
	}

	/**
	 * @return \League\Flysystem\AdapterInterface|AdapterInterface
	 */
	protected function getRealAdapter()
	{
		$adapter = $this->getAdapter();

		if ($adapter instanceof CachedAdapter) {
			return $adapter->getAdapter();
		}

		return $adapter;
	}

	/**
	 * @param string $connection
	 * @return \League\Flysystem\FilesystemInterface
	 */
	public static function connection($connection)
	{
		$adapter = StorageAdapterFactory::makeCached($connection);
		return new static($adapter);
	}
}