from fixtures.builder import ConsistencyVariantFixtureBuilder


def build():
    ConsistencyVariantFixtureBuilder('Simple', { 'use_snapshot_hashes': True })\
        .create_target('testtarget.txt')\
        .publish(with_client=True)
