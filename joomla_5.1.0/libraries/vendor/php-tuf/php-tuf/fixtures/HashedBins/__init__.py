# This fixture creates targets named a.txt through z.txt, and
# distributes them across 8 hashed bin delegations. This uses
# the "classic" (i.e., not succinct) form of hashed bins.

import string

from fixtures.builder import FixtureBuilder
from tuf import roledb

def build():
    builder = FixtureBuilder('HashedBins', { 'use_snapshot_length': True })\
        .publish(with_client=True)

    list_of_targets = []
    for c in list(string.ascii_lowercase):
        name = c + '.txt'
        builder.create_target(name, signing_role=None)
        list_of_targets.append(name)

    # We need at least one key that will sign the targets in the hashed bins.
    public_key, private_key = builder._import_key()

    # Create the hashed bins.
    builder.repository.targets.delegate_hashed_bins(list_of_targets, [public_key], 8)

    # Assign the targets we've created to those hashed bins. TUF determines which
    # target goes in which bin.
    for name in list_of_targets:
        builder.repository.targets.add_target_to_bin(name, 8)

    # Ensure the delegated roles that manage the hashed bins can actually be signed.
    # It's weird, but for some reason this is not done by delegate_hashed_bins().
    for role in builder.repository.targets.get_delegated_rolenames():
        builder.repository.targets(role).load_signing_key(private_key)

    # Make all the delegated roles terminating.
    targets_role_info = roledb.get_roleinfo('targets', 'HashedBins')
    for i in range(len(targets_role_info['delegations']['roles'])):
        targets_role_info['delegations']['roles'][i]['terminating'] = True
    roledb.update_roleinfo('targets', targets_role_info, repository_name='HashedBins')

    # Publish these changes on the server side.
    builder.invalidate()
    builder.publish()
