<?php namespace Superbalist\Shared\Libs\Storage;

class GoogleAuthOAuth2 extends \Google_Auth_OAuth2 {

	/**
	 * @var \Google_Auth_AssertionCredentials
	 */
	protected $creds;

	/**
	 * @param \Google_Auth_AssertionCredentials $creds
	 */
	public function setAssertionCredentials(\Google_Auth_AssertionCredentials $creds)
	{
		$this->creds = $creds;
		parent::setAssertionCredentials($creds);
	}

	/**
	 * @return \Google_Auth_AssertionCredentials
	 */
	public function getAssertionCredentials()
	{
		return $this->creds;
	}
}
