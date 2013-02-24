<?php
namespace Craft;

Craft::requirePackage(CraftPackage::Users);

/**
 *
 */
class UserGroupRecord extends BaseRecord
{
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'usergroups';
	}

	/**
	 * @return array
	 */
	public function defineAttributes()
	{
		return array(
			'name'   => array(AttributeType::Name, 'required' => true),
			'handle' => array(AttributeType::Handle, 'required' => true),
		);
	}

	/**
	 * @return array
	 */
	public function defineRelations()
	{
		return array(
			'users' => array(static::MANY_MANY, 'UserRecord', 'usergroups_users(groupId, userId)'),
		);
	}
}
