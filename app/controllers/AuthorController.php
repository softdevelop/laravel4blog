<?php

class AuthorController extends AuthorizedController {

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->check_permissions();
	}
	
	private function check_permissions()
	{
		// Find the user using the user id
		//$user = Sentry::findUserByID(Sentry::getUser()->id);
		$user = Sentry::getUser();
		if($user != NULL) {
			// Check if the user has the 'author' permission
			if (!$user->hasAccess('author'))
			{
				return Redirect::to(route('home'))->with('error', 'You not have permission for this page!')->send();
			}
		} else {
			return Redirect::to(route('signin'))->with('error', 'Please login first!')->send();
		}
	}
	/**
	 * Encodes the permissions so that they are form friendly.
	 *
	 * @param  array  $permissions
	 * @param  bool   $removeSuperUser
	 * @return void
	 */
	protected function encodeAllPermissions(array &$allPermissions, $removeSuperUser = false)
	{
		foreach ($allPermissions as $area => &$permissions)
		{
			foreach ($permissions as $index => &$permission)
			{
				if ($removeSuperUser == true and $permission['permission'] == 'superuser')
				{
					unset($permissions[$index]);
					continue;
				}

				$permission['can_inherit'] = ($permission['permission'] != 'superuser');
				$permission['permission']  = base64_encode($permission['permission']);
			}

			// If we removed a super user permission and there are
			// none left, let's remove the group
			if ($removeSuperUser == true and empty($permissions))
			{
				unset($allPermissions[$area]);
			}
		}
	}

	/**
	 * Encodes user permissions to match that of the encoded "all"
	 * permissions above.
	 *
	 * @param  array  $permissions
	 * @return void
	 */
	protected function encodePermissions(array &$permissions)
	{
		$encodedPermissions = array();

		foreach ($permissions as $permission => $access)
		{
			$encodedPermissions[base64_encode($permission)] = $access;
		}

		$permissions = $encodedPermissions;
	}

	/**
	 * Decodes user permissions to match that of the encoded "all"
	 * permissions above.
	 *
	 * @param  array  $permissions
	 * @return void
	 */
	protected function decodePermissions(array &$permissions)
	{
		$decodedPermissions = array();

		foreach ($permissions as $permission => $access)
		{
			$decodedPermissions[base64_decode($permission)] = $access;
		}

		$permissions = $decodedPermissions;
	}

}
