@replay
Feature:
  Background:
    Given an EngineBlock instance configured with JSON data
    And an Identity Provider named "Replay Idp"
    And a Service Provider named "Replay SP"

  Scenario: Replay login requests
    Given SP "Replay SP" is configured to generate a AuthnRequest like the one at "fixtures/replay/sp.request.log"
    And SP "Replay SP" does not require consent
    And IdP "Replay Idp" is configured to return a Response like the one at "fixtures/replay/idp.response.log"
    And SP "Replay SP" may only access "Replay Idp"
    And EngineBlock is expected to send a AuthnRequest like the one at "fixtures/replay/eb.request.log"
    And EngineBlock is expected to send a Response like the one at "fixtures/replay/eb.response.log"
    When I log in at "Replay SP"
    And print last response
    And I follow "GO"
    Then the request should be compared with the one at "fixtures/replay/eb.request.log"
    And print last response
    And I press "GO"
    And print last response
    And I press "Submit"
    And print last response
    Then the response should be compared with the one at "fixtures/replay/eb.response.log"
