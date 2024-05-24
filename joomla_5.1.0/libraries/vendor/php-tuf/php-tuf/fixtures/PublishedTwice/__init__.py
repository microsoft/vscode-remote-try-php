from fixtures.builder import ConsistencyVariantFixtureBuilder


def build(rotate_keys=None):
    """
    Generates a TUF test fixture that publishes twice -- once on the client,
    and twice on the server -- and, in between those two publications, can
    optionally rotate the keys of a given role.
    """
    name = 'PublishedTwice'
    if rotate_keys is not None:
        name += 'WithRotatedKeys_' + rotate_keys

    fixture = ConsistencyVariantFixtureBuilder(name, { 'use_snapshot_hashes': True, 'use_snapshot_length': True, 'use_timestamp_hashes': True, 'use_timestamp_length': True })\
        .publish(with_client=True)
    fixture.create_target('test.txt')

    if rotate_keys is not None:
        fixture.add_key(rotate_keys)\
            .revoke_key(rotate_keys, key_index=0)
    fixture.publish()
