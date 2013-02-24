<?php
namespace Craft;

/**
 * Validates the required Site attributes for the installer.
 */
class SiteSettingsModel extends BaseModel
{
	/**
	 * @return array
	 */
	public function defineAttributes()
	{
		return array(
			'siteName' => array(AttributeType::Name, 'required' => true),
			'siteUrl'  => array(AttributeType::Url, 'required' => true)
		);
	}
}
