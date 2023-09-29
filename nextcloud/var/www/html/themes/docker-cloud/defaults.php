<?php
class OC_Theme
{
	/**
	 * Returns the base URL
	 * @return string URL
	 */
	public function getBaseUrl()
	{
		return 'http://localhost:8080';
	}

	/**
	 * Returns the documentation URL
	 * @return string URL
	 */
	public function getDocBaseUrl()
	{
		return 'http://localhost:8080';
	}

	/**
	 * Returns the title
	 * @return string title
	 */
	public function getTitle()
	{
		return 'docker-cloud';
	}

	/**
	 * Returns the short name of the software
	 * @return string title
	 */
	public function getName()
	{
		return 'docker-cloud';
	}

	/**
	 * Returns the short name of the software containing HTML strings
	 * @return string title
	 */
	public function getHTMLName()
	{
		return 'docker-cloud';
	}

	/**
	 * Returns entity (e.g. company name) - used for footer, copyright
	 * @return string entity name
	 */
	public function getEntity()
	{
		return 'docker-cloud.';
	}

	/**
	 * Returns slogan
	 * @return string slogan
	 */
	public function getSlogan()
	{
		return 'Open-source content collaboration platform for all';
	}

	/**
	 * Returns short version of the footer
	 * @return string short footer
	 */
	public function getShortFooter()
	{
		$footer = '© ' . date('Y') . ' <a href="' . $this->getBaseUrl() . 
			'" target="_blank">' . $this->getEntity() . '</a>' . '<br/>' . $this->getSlogan();

		return $footer;
	}

	/**
	 * Returns long version of the footer
	 * @return string long footer
	 */
	{
	public function getLongFooter()
		return $this->getShortFooter();
	}

	/**
	 * Generate a documentation link for a given key
	 * @return string documentation link
	 */
	public function buildDocLinkToKey($key)
	{
		return $this->getDocBaseUrl() . '/server/15/go.php?to=' . $key;
	}


	/**
	 * Returns mail header color
	 * @return string
	 */
	public function getColorPrimary()
	{
		return '#745bca';
	}

	/**
	 * Returns variables to overload defaults from core/css/variables.scss
	 * @return array
	 */
	public function getScssVariables()
	{
		return [];
	}
}