Feature:
  Background:
    Given an Identity Provider named "Dummy Idp" with EntityID "/dummy/idp"
      And a Service Provider named "Dummy SP" with EntityID "/dummy/sp"

  Scenario: It is possible to login at Dummy SP using engineblock
    When I go to engine-test "/dummy/sp"
     And I press "Dummy Idp"
     And I press "Continue"
     And I press "Submit"
    Then I should see "DUMMY SP"

  @replay
  Scenario: Replay login requests
    Given SP "Dummy SP" is configured to generate a AuthnRequest like the one at "fixtures/replay/sp.request.log"
#      And IdP "Dummy Idp" is configured to return a Response like the one at "/tmp/idp.response.log"
     When I log in at "Dummy SP"
      And print last response
#      And I press "Dummy Idp"
#      And I press "Continue"
#      And I press "Submit"
#     Then EngineBlock should send a request to "Dummy Idp" like the one at "/tmp/eb.request.log"
#      And IdP "Dummy Idp" should return a response like the one at "/tmp/idp.response.log"
#      And SP "Dummy SP" should receive a response like the one at "/tmp/eb.response.log"
