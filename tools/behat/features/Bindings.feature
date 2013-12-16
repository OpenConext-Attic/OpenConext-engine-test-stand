Feature:
  In order to realize a named business value
  As an IdP or SP
  I want to send SAML Requests  / Responses in a variety of ways

  Scenario: EngineBlock accepts AuthnRequests using HTTP-POST binding
    Given Dummy Sp is configured to use the "PostRequest" testcase
     When I go to engine-test "/dummy/sp"
      And I press "Continue"
     Then I should see "Dummy Idp"

  Scenario: EngineBlock accepts AuthnRequests using HTTP-Redirect binding
    Given Dummy Idp is configured to use the "RedirectResponse" testcase
     When I go to engine-test "/dummy/sp"
      And I press "Dummy Idp"
     Then the url should match "consume-assertion"

  Scenario: EngineBlock accepts Signed AuthnRequests using HTTP-POST binding
    Given Dummy Sp is configured to use the "SignedPostRequest" testcase
     When I go to engine-test "/dummy/sp"
      And I press "Continue"
     Then I should see "Dummy Idp"
