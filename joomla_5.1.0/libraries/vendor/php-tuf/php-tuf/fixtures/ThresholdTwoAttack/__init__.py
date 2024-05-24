from fixtures.builder import FixtureBuilder

import os


def build():
    _build(True)
    _build(False)

def _build(consistent):
    if consistent is True:
        suffix = 'consistent'
    else:
        suffix = 'inconsistent'

    name = os.path.join('ThresholdTwoAttack', suffix)

    fixture = FixtureBuilder(name)\
        .add_key('timestamp')
    fixture._role('timestamp').threshold = 2
    fixture.repository.mark_dirty(['timestamp'])
    fixture.publish(with_client=True, consistent=consistent)
    fixture.repository.mark_dirty(['timestamp'])
    fixture.publish(with_client=True, consistent=consistent)

    # By exporting the repo but not the client, this gives us a new revision
    # that's ready to alter. If we alter a version the client is already
    # aware of, it may not pick up this new, altered version.
    fixture.repository.mark_dirty(['timestamp'])
    fixture.publish(consistent=consistent)

    fixture.add_key('timestamp')

    timestamp = fixture.read('timestamp.json')
    timestamp["signatures"][1] = {
        'keyid': fixture._keys['timestamp']['public'][2]['keyid'],
        # This is the SHA-512 hash of the sentence "This is just a random string".
        'sig': 'd1f9ee4f5861ad7b8be61c0c00f3cd4353cee60e70db7d6fbeab81b75e6a5e3871276239caf93d09e9cd406ba764c31abe00e95f2553a3cb543874cb6e7d1545'
    }

    fixture.write('timestamp.json', timestamp)

    # We could also alter the versioned (N.timestamp.json), but the spec
    # considers these as optional, so we can expect this alteration to be
    # sufficient.
