Feature:
  In order to explain my login problem's to the helpdesk
  As a user
  I need to see useful error information when something goes wrong

  Background:
    Given an EngineBlock instance on "demo.openconext.org"
      And no registered SPs
      And no registered Idps
      And an Identity Provider named "Dummy Idp"
      And the IdP uses a blacklist for access control
      And a Service Provider named "Dummy SP"
      And a Service Provider named "Connected SP"
      And SP "Connected SP" uses a blacklist of access control
      And a Service Provider named "Unconnected SP"
      And SP "Unconnected SP" uses a whitelist for access control
      And an unregistered Service Provider named "Unregistered SP"

  Scenario: I log in at my Identity Provider, but something goes wrong and it returns an error response.
    Given the IdP is configured to always return Responses with StatusCode Requester/InvalidNameIDPolicy
      And the IdP is configured to always return Responses with StatusMessage "NameIdPolicy is invalid"
     When I log in at "Dummy SP"
      And I pass through EngineBlock
      And I pass through the IdP
     Then I should see "Idp error"
      And I should see "InvalidNameIDPolicy"
      And I should see "NameIdPolicy is invalid"
      And I should see "Timestamp:"
      And I should see "Unique Request Id:"
      And I should see "User Agent:"
      And I should see "IP Address:"
      And I should see "Service Provider:"
      And I should see "Identity Provider:"

  Scenario: I log in at my Identity Provider, but it has changed (private/public) keys without notifying OpenConext
    Given the IdP uses the private key at "src/OpenConext/Component/EngineTestStand/Resources/keys/rolled-over.key"
      And the IdP uses the certificate at "src/OpenConext/Component/EngineTestStand/Resources/keys/rolled-over.crt"
     When I log in at "Dummy SP"
      And I pass through EngineBlock
      And I pass through the IdP
     Then I should see "Invalid signature on Idp response"
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

  Scenario: An Identity Provider misrepresents it's entityId and is thus not recognized by EB
    Given the IdP thinks it's EntityID is "https://wrong.example.edu/metadata"
     When I log in at "Dummy SP"
      And I pass through EngineBlock
      And I pass through the IdP
     Then I should see "Unknown application"
      And I should see "Timestamp:"
      And I should see "Unique Request Id:"
      And I should see "User Agent:"
      And I should see "IP Address:"
      And I should see "Service Provider:"
      And I should see "Identity Provider:"
      And I should see "https://wrong.example.edu/metadata"

  Scenario: An Identity Provider tries to send a response over HTTP-Redirect, violating the spec
    Given IdP Sp uses the HTTP Redirect Binding
     When I log in at "Dummy SP"
      And I pass through EngineBlock
     Then I should see "Invalid ACS Binding Type"
      And I should see "Timestamp:"
      And I should see "Unique Request Id:"
      And I should see "User Agent:"
      And I should see "IP Address:"
      And I should see "Service Provider:"
      And I should see "Identity Provider:"


#  Scenario: An Identity Provider sends a response without a SHO
#  Scenario: An Identity Provider sends a response without a uid
#
#  Scenario: I try an unsolicited login (at EB) but mess up by not specifying a location
#  Scenario: I try an unsolicited login (at EB) but mess up by not specifying a binding
#  Scenario: I try an unsolicited login (at EB) but mess up by not specifying an invalid index
#
#  Scenario: An SP sends a AuthnRequest transparently for a IdP that doesn't exist
#
#  Scenario: I don't give consent to release my attributes to a Service Provider
#
#  Scenario: I visit the SSO location without a SAMLRequest
#  Scenario: I visit the ACS location without a SAMLResponse
#  Scenario: I visit the SSO location with a bad SAMLRequest
#  Scenario: I visit the ACS location with a bad SAMLResponse
#
#  Scenario: I lose my 'main' session cookie.
#
#  Scenario: An attribute manipulation determines that a user may not continue
#
#  Scenario: An Identity Provider dates it's Assertions in the future.
#  Scenario: I want to log in to a service but am not a member of the appropriate VO