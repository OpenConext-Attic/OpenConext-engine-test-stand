@replay
Feature:
  Background:
    Given an EngineBlock instance configured with JSON data
    And an Identity Provider named "Replay Idp"
    And a Service Provider named "Replay SP"

  Scenario: Replay login requests
    Given SP "Replay SP" is configured to generate a AuthnRequest like the one at "fixtures/replay/sp.request.log"
    And SP "Replay SP" does not require consent
    And SP "Replay SP" may run in transparent mode, if indicated in "fixtures/replay/session.log"
    And IdP "Replay Idp" is configured to return a Response like the one at "fixtures/replay/idp.response.log"
    And SP "Replay SP" may only access "Replay Idp"
    And EngineBlock is expected to send a AuthnRequest like the one at "fixtures/replay/eb.request.log"
    And EngineBlock is expected to send a Response like the one at "fixtures/replay/eb.response.log"
    And I print the configured ids
    When I trigger the login (either at "Replay SP" or unsollicited at EB)
    And print last response
    And I follow the EB debug screen to the IdP
    And print last response
    Then the request should be compared with the one at "fixtures/replay/eb.request.log"
    And I press "GO"
    And print last response
    And I press "Submit"
    And print last response
    Then the response should be compared with the one at "fixtures/replay/eb.response.log"
