from fixtures.builder import ConsistencyVariantFixtureBuilder


def build():
    fixture = ConsistencyVariantFixtureBuilder('Delegated', { 'use_timestamp_length': False })\
        .create_target('testtarget.txt')\
        .publish(with_client=True)\
        .delegate('unclaimed', ['level_1_*.txt'])\
        .create_target('level_1_target.txt', signing_role='unclaimed')\
        .publish(with_client=True)
    # === Point of No Return ===
    # Past this point, we don't re-export the client. This supports testing the
    # client's own ability to pick up and trust new data from the repository.
    fixture.add_key('targets')\
        .add_key('snapshot')\
        .invalidate()\
        .publish()\
        .revoke_key('targets')\
        .revoke_key('snapshot')\
        .invalidate()\
        .publish()
