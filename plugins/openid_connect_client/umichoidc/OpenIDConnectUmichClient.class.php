<?php


class OpenIDConnectUmichClient extends OpenIDConnectClientBase {

  /**
   * {@inheritdoc}.
   */
  public function settingsForm() {
    $form = parent::settingsForm();
    $roles = user_roles();
    $role_list = [];
    unset($roles['anonymous user'], $roles['authenticated user'], $roles['administrator']);
//    foreach ($roles as $i => $v) {
//      if (!in_array($i, ['anonymous', 'authenticated', 'administrator'])) {
//        $role_list[$v->label()] = $v->label();
//      }
//
//    }
    $form['roles'] = [
      '#type' => 'select',
      '#title' => t('OIDC managed Roles'),
      '#options' => $roles,
      '#default_value' => $this->getSetting('umichoidc_roles'),
      '#multiple' => TRUE,
      '#description' => 'Your role name must match your m-community group name.  Using this feature will override manual assigment of selected roles.',
    ];
    $form['testshib'] = [
      '#type' => 'checkbox',
      '#title' => t('Use testing instance of the IDP'),
      '#description' => 'Only check this box if directed to do so by ITS',
      '#default_value' => $this->getSetting('umichoidc_testshib'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndpoints() {
    if ($this->getSetting('umichoidc_testshib') == 1) {
      $service = json_decode(file_get_contents("https://shib-idp-staging.dsc.umich.edu/.well-known/openid-configuration"));
    }
    else {
      $service = json_decode(file_get_contents("https://shibboleth.umich.edu/.well-known/openid-configuration"));
    }
    return [
      'authorization' => $service->authorization_endpoint,
      'token' => $service->token_endpoint,
      'userinfo' => $service->userinfo_endpoint,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function authorize($scope = '') {
    parent::authorize('openid email edumember profile  account_type');
  }

  /**
   * {@inheritdoc}
   */
  public function decodeIdToken($id_token) {
    return [];
  }

}
