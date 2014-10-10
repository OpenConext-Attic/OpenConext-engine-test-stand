Feature:
  In order to realize a named business value
  As an IdP or SP
  I want to send SAML Requests  / Responses in a variety of ways

  Background:
    Given an EngineBlock instance on "demo.openconext.org"
      And an Identity Provider named "Dummy Idp"
      And a Service Provider named "Dummy SP"

  Scenario: EngineBlock accepts AuthnRequests using HTTP-POST binding
    Given the Sp uses the HTTP POST Binding
     When I log in at "Dummy SP"
      And I pass through the Sp
      And I pass through EngineBlock
      And I pass through the IdP
      And I pass through EngineBlock
     Then the url should match "Dummy%20SP/acs"

  Scenario: EngineBlock accepts AuthnRequests using HTTP-Redirect binding
    Given the Sp uses the HTTP Redirect Binding
     When I log in at "Dummy SP"
      And I pass through EngineBlock
      And I pass through the IdP
      And I pass through EngineBlock
     Then the url should match "Dummy%20SP/acs"

  Scenario: EngineBlock accepts Signed AuthnRequests using HTTP-POST binding
    Given the Sp uses the HTTP POST Binding
      And the Sp signs it's requests
     When I log in at "Dummy SP"
      And I pass through the Sp
      And I pass through EngineBlock
      And I pass through the IdP
      And I pass through EngineBlock
     Then the url should match "Dummy%20SP/acs"
