from fixtures.builder import ConsistencyVariantFixtureBuilder


def build():
    """
    Generates a TUF test fixture that publishes twice -- once on the client,
    and twice on the server -- with targets length information, but no
    snapshot length information.
    """
    ConsistencyVariantFixtureBuilder('TargetsLengthNoSnapshotLength', { 'use_timestamp_length': False, 'use_snapshot_length': True })\
    .publish(with_client=True)\
    .publish()
