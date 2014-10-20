Feature:
  In order to increase my level of assurance
  As a user
  I need EB to proxy for my Step Up proxy

  Background:
    Given an EngineBlock instance on "demo.openconext.org"
      And no registered SPs
      And no registered Idps
      And an Identity Provider named "AlwaysAuth"
      And an Identity Provider named "StepUpOnlyAuth"
      And an Identity Provider named "LoaOnlyAuth"
      And an Identity Provider named "CombinedAuth"
      And a Service Provider named "Step Up"
      And a Service Provider named "Loa SP"
      And IdP "AlwaysAuth" uses a blacklist
      And IdP "StepUpOnlyAuth" uses a whitelist
      And IdP "StepUpOnlyAuth" whitelists SP "Step Up"
      And IdP "LoaOnlyAuth" uses a whitelist
      And IdP "LoaOnlyAuth" whitelists SP "Loa SP"
      And IdP "CombinedAuth" uses a whitelist
      And IdP "CombinedAuth" whitelists SP "Step Up"
      And IdP "CombinedAuth" whitelists SP "Loa SP"
      And SP "Step Up" uses a whitelist for access control
      And SP "Step Up" whitelists IdP "AlwaysAuth"
      And SP "Step Up" whitelists IdP "StepUpOnlyAuth"
      And SP "Step Up" whitelists IdP "CombinedAuth"
      And SP "Loa SP" uses a whitelist for access control
      And SP "Loa SP" whitelists IdP "AlwaysAuth"
      And SP "Loa SP" whitelists IdP "LoaOnlyAuth"
      And SP "Loa SP" whitelists IdP "CombinedAuth"

  Scenario: User logs in to the SP without a proxy and wayf shows relevant Identity Providers
    When I log in at "Loa SP"
    Then I should see "AlwaysAuth"
     And I should see "CombinedAuth"
     And I should see "LoaOnlyAuth"
     And I should not see "StepUpOnlyAuth"

  Scenario: User logs in to the proxy without a SP and wayf shows relevant Identity Providers
     When I log in at "Step Up"
     Then I should see "AlwaysAuth"
      And I should see "CombinedAuth"
      And I should see "StepUpOnlyAuth"
      And I should not see "LoaOnlyAuth"

  Scenario: User logs in via untrusted proxy accesses discovery for unknown SP
    Given SP "Step Up" is authenticating and uses RequesterID "https://example.edu/saml2/metadata"
     When I log in at "Step Up"
     Then I should see "AlwaysAuth"
      And I should see "CombinedAuth"
      And I should see "StepUpOnlyAuth"
      And I should not see "LoaOnlyAuth"

  Scenario: User logs in via untrusted proxy accesses discovery for known SP, sees less IdPs
    Given SP "Step Up" is authenticating for SP "Loa SP"
     When I log in at "Step Up"
     Then I should see "AlwaysAuth"
      And I should see "CombinedAuth"
      # In order to gain access to an IdP through a SP Proxy, the SP Proxy also needs access to the IdP
      And I should not see "LoaOnlyAuth"
      And I should not see "StepUpOnlyAuth"

  Scenario: User logs in via untrusted proxy accesses discovery for known SP, sees less IdPs
    Given SP "Step Up" is authenticating for SP "Loa SP"
     When I log in at "Step Up"
     Then I should see "AlwaysAuth"
      And I should see "CombinedAuth"
      # In order to gain access to an IdP through a SP Proxy, the SP Proxy also needs access to the IdP
      And I should not see "LoaOnlyAuth"
      And I should not see "StepUpOnlyAuth"

  Scenario: User logs in via untrusted proxy for destination without consent and sees consent for proxy anyway
    Given SP "Step Up" is authenticating for SP "Loa SP"
     When I log in at "Step Up"
      And I press "AlwaysAuth"
      And I pass through EngineBlock
      And I pass through the IdP
     Then I should see "we must share the following information"
      And I should see "Step Up"
      And I should not see "Loa SP"

  Scenario: User logs in via trusted proxy and sees consent for the destination
    Given SP "Step Up" is authenticating for SP "Loa SP"
      And SP "Step Up" is a trusted proxy
      # Test to see that we don't trust trusted proxies without request signing
      #And SP "Step Up" signs it's requests
     When I log in at "Step Up"
      And I press "AlwaysAuth"
      And I pass through EngineBlock
      And I pass through the IdP
     Then I should see "we must share the following information"
      And I should see "Step Up"
      And I should not see "Loa SP"

  Scenario: User logs in via trusted proxy and sees consent for the destination
    Given SP "Step Up" is authenticating for SP "Loa SP"
      And SP "Step Up" is a trusted proxy
      And SP "Step Up" signs it's requests
     When I log in at "Step Up"
      And I press "AlwaysAuth"
      And I pass through EngineBlock
      And I pass through the IdP
     Then I should see "we must share the following information"
      And I should see "Loa SP"
      And I should not see "Step Up"

#  Scenario: User logs in via a proxy proxy and sees consent for the proxy proxy, but not the destination (only 1 level)
#  Scenario: User logs in via trusted proxy without consent and sees no consent
#  Scenario: User logs in via trusted proxy for destination without consent and sees no consent
#  Scenario: User logs in via trusted proxy and attribute manipulation for proxy and destination are executed
#  Scenario: User logs in via trusted proxy and attribute release policy for proxy and destination are executed
#  Scenario: User logs in via trusted proxy and I don't see arp disallowed attributes in consent
#  Scenario: User logs in via trusted proxy and I get a NameID for the destination