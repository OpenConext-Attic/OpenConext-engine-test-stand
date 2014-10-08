Feature:
  In order to explain my login problem's to the helpdesk
  As a user
  I need to see useful error information when something goes wrong

  Background:
    Given an Identity Provider named "Dummy Idp"
      And the IdP uses a blacklist for access control
      And a Service Provider named "Connected SP"
      And Sp "Connected SP" uses a blacklist of access control
      And a Service Provider named "Unconnected SP"
      And Sp "Unconnected SP" uses a whitelist for access control
      And an unregistered Service Provider named "Unregistered SP"

  Scenario: I log in at my Identity Provider, but something goes wrong and it returns an error response.
    Given the IdP is configured to always return Responses with StatusCode Requester/InvalidNameIDPolicy
      And the IdP is configured to always return Responses with StatusMessage "NameIdPolicy is invalid"
     When I log in at "Dummy SP"
      And I press "Submit"
      And I press "GO"
     Then I should see "Idp error"
      And I should see "Status Code: urn:oasis:names:tc:SAML:2.0:status:InvalidNameIDPolicy"
      And I should see "Status Message: NameIdPolicy is invalid"
      And I should see "Timestamp:"
      And I should see "Unique Request Id:"
      And I should see "User Agent:"
      And I should see "IP Address:"
      And I should see "Service Provider:"
      And I should see "Identity Provider:"

  Scenario: I log in at my Identity Provider, but it has changed (private/public) keys without notifying OpenConext
    Given the IdP uses the private key at "Resources/keys/rolled-over.key"
      And the IdP uses the certificate at "Resources/keys/rolled-over.crt"
     When I log in at "Dummy SP"
      And I press "Submit"
      And I press "GO"
      And I press "Submit"
     Then I should see "Invalid Idp response"
      And I should see "Timestamp:"
      And I should see "Unique Request Id:"
      And I should see "User Agent:"
      And I should see "IP Address:"
      And I should see "Service Provider:"
      And I should see "Identity Provider:"

  Scenario: I want to log on, but this Service Provider may not access any Identity Providers
    When I log in at "Unconnected SP"
    Then I should see "No Identity Providers found"
     And I should see "Timestamp:"
     And I should see "Unique Request Id:"
     And I should see "User Agent:"
     And I should see "IP Address:"
     And I should see "Service Provider:"
     And I should not see "Identity Provider:"

  Scenario: I want to log on but this Service Provider is not yet registered at OpenConext
    When I log in at "Unregistered SP"
    Then I should see "Unknown application"
     And I should see "Timestamp:"
     And I should see "Unique Request Id:"
     And I should see "User Agent:"
     And I should see "IP Address:"
     And I should see "Service Provider:"
     And I should not see "Identity Provider:"

  #test:
    # - The assertion is not valid yet. This happens when the clock on the IdP is running ahead.
    # - We cannot locate the session identifier of the user. This happens when: a user is directed to another LB or we loose their session info for some other reason.
    # - The ACL does not allow a user to access the service: This happens with SPs the use our transparent (idps) metadata and send an AuthnRequest for an IdP this is not allowed access to the SP.
    # - The user sent us a SAML assertion, but did not send the session cookie (so we cannot locate their session). This happens e.g. with login in an iframe with third party cookies disabled or other situations where the security settings in a browser prevent a cookie from being sent.
