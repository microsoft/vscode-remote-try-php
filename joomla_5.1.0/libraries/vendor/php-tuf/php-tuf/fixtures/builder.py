"""
Contains a class to help build fixtures programmatically.
"""

from securesystemslib import formats, signer
from tuf import repository_tool, roledb

import json
import os
import shutil
from dirhash import dirhash


class FixtureBuilder:

    def __init__(self, name, tuf_arguments={ 'use_snapshot_length': False }):
        self.dir = os.path.join(os.path.dirname(__file__), name)

        # The index of the next key pair (in the keys/ directory) to use when initializing
        # a role.
        self._key_index = 0
        # The keychain, containing all public and private keys. The dictionary
        # keys are role names, and each item is a dictionary with 'public' and
        # 'private' members, which are lists of public and private keys.
        self._keys = {}
        # The directory of server-side metadata (and targets).
        self._server_dir = os.path.join(self.dir, 'server')

        # If a directory of server-side metadata already exists, remove it.
        if os.path.isdir(self._server_dir):
            shutil.rmtree(self._server_dir)

        self.repository = repository_tool.create_new_repository(self._server_dir, name, **tuf_arguments)
        self.repository.status()

        # Initialize the basic TUF roles.
        self.add_key('root')
        self.add_key('targets')
        self.add_key('snapshot')
        self.add_key('timestamp')

        self.repository.status()

    def __del__(self):
        # Create a hash for the generated fixture.
        with open(self.dir + "/hash.txt", "w") as hash_file:
            hash_file.write(dirhash(self.dir, 'sha256', ignore=["__init__.py", "client_versions.ini", "hash.txt"]))

    def _role(self, name):
        """Loads a role object for a specific role."""
        try:
            return getattr(self.repository, name)
        except AttributeError:
            return self.repository.targets(name)

    def delegate(self, role_name, paths, parent='targets', path_hash_prefixes=None, terminating=False):
        """Creates a delegated role."""
        self._role(parent).delegate(role_name, [], paths, path_hash_prefixes=path_hash_prefixes, terminating=terminating)
        self.add_key(role_name)
        return self

    def add_key(self, role_name):
        """Loads a key pair from disk and assigns it to a given role."""
        (public_key, private_key) = self._import_key()

        role = self._role(role_name)
        role.add_verification_key(public_key)
        role.load_signing_key(private_key)

        if role_name not in self._keys:
            self._keys[role_name] = {'public': [], 'private': []}

        self._keys[role_name]['public'].append(public_key)
        self._keys[role_name]['private'].append(private_key)

        self._mark_dirty(role_name)

        return self

    def revoke_key(self, role_name, key_index=0):
        """Revokes a key pair from a given role."""
        public_key = self._keys[role_name]['public'].pop(key_index)
        self._role(role_name).remove_verification_key(public_key)
        self._keys[role_name]['private'].pop(key_index)

        self._mark_dirty(role_name)

        return self

    def _mark_dirty(self, role_name):
        """Marks a role as dirty, along with its parent role."""
        self.repository.mark_dirty([role_name])

        if role_name in roledb.TOP_LEVEL_ROLES:
            self.repository.mark_dirty(['root'])
        else:
            self.repository.mark_dirty(['targets'])

    def _import_key(self):
        """Loads a key pair from the keys/ directory."""
        keys_dir = os.path.join(os.path.dirname(__file__), 'keys')
        private_key = os.path.join(keys_dir, str(self._key_index)) + '_key'
        public_key = '{}.pub'.format(private_key)

        self._key_index = self._key_index + 1

        return (
            repository_tool.import_ed25519_publickey_from_file(public_key),
            repository_tool.import_ed25519_privatekey_from_file(private_key, password='pw')
        )

    def invalidate(self):
        """Marks the four top-level TUF roles as dirty."""
        self.repository.mark_dirty(roledb.TOP_LEVEL_ROLES)
        return self

    def add_target(self, filename, signing_role='targets'):
        """Adds an existing target file and signs it."""
        # @todo Just use add_target or add_targets consistently. This is only
        # here while fixtures are being ported to FixtureBuilder, to maintain
        # consistency with previously generated fixtures.
        if signing_role == 'targets':
            self._role('targets').add_targets([filename])
        else:
            self._role(signing_role).add_target(filename)
        self.repository.mark_dirty(['snapshot', 'targets', 'timestamp', signing_role])

        return self

    def create_target(self, filename, contents=None, signing_role='targets'):
        """Creates a signed target file with arbitrary contents."""
        if contents is None:
            contents = 'Contents: ' + filename

        path = os.path.join(self._server_dir, 'targets', filename)
        with open(path, 'w') as f:
            f.write(contents)

        if signing_role is not None:
            self.add_target(filename, signing_role)

        return self

    def publish(self, with_client=False, consistent=True):
        """Writes the TUF metadata to disk."""
        self.repository.writeall(consistent_snapshot=consistent)

        staging_dir = os.path.join(self._server_dir, 'metadata.staged')
        live_dir = os.path.join(self._server_dir, 'metadata')
        shutil.copytree(staging_dir, live_dir, dirs_exist_ok=True)

        if with_client:
            client_dir = os.path.join(self.dir, 'client')
            # If a directory of client-side metadata already exists, remove it.
            if os.path.isdir(client_dir):
                shutil.rmtree(client_dir)

            repository_tool.create_tuf_client_directory(self._server_dir, client_dir)

        return self

    def read(self, filename):
        """Returns the parsed contents of an existing metadata file."""
        path = os.path.join(self._server_dir, 'metadata', filename)

        with open(path, 'r') as f:
            return json.load(f)

    def write(self, filename, data):
        path = os.path.join(self._server_dir, 'metadata', filename)

        with open(path, 'w') as f:
            json.dump(data, f, indent=1, separators=(',', ': '), sort_keys=True)

    def write_signed(self, filename, data, signing_role):
        """Writes arbitrary metadata, signed with a given role's keys, to a file."""
        self.write(filename, {
            'signatures': self._sign(data, signing_role),
            'signed': data
        })

    def _sign(self, data, signing_role):
        """Signs arbitrary data using a given role's keys."""
        signatures = []

        # Encode the data to canonical JSON, which is what we will actually sign.
        data = str.encode(formats.encode_canonical(data))

        # Loop through the signing role's private keys and use each one to sign
        # the canonical JSON representation of the data.
        for key in self._keys[signing_role]['private']:
            signature = signer.SSlibSigner(key).sign(data)
            signatures.append(signature.to_dict())

        return signatures


class ConsistencyVariantFixtureBuilder:

    def __init__(self, name, tuf_arguments={ 'use_snapshot_length': False }):
        self.fixtures = [
            FixtureBuilder(os.path.join(name, 'consistent'), tuf_arguments),
            FixtureBuilder(os.path.join(name, 'inconsistent'), tuf_arguments)
        ]

    def delegate(self, role_name, paths, parent='targets', path_hash_prefixes=None, terminating=False):
        for fixture in self.fixtures:
            fixture.delegate(role_name, paths, parent, path_hash_prefixes, terminating)
        return self

    def add_key(self, role_name):
        for fixture in self.fixtures:
            fixture.add_key(role_name)
        return self

    def revoke_key(self, role_name, key_index=0):
        for fixture in self.fixtures:
            fixture.revoke_key(role_name, key_index)
        return self

    def invalidate(self):
        for fixture in self.fixtures:
            fixture.invalidate()
        return self

    def add_target(self, filename, signing_role='targets'):
        for fixture in self.fixtures:
            fixture.add_target(filename, signing_role)
        return self

    def create_target(self, filename, contents=None, signing_role='targets'):
        for fixture in self.fixtures:
            fixture.create_target(filename, contents, signing_role)
        return self

    def publish(self, with_client=False):
        self.fixtures[0].publish(with_client, consistent=True)
        self.fixtures[1].publish(with_client, consistent=False)
        return self

    def read(self, filename):
        return [
            self.fixtures[0].read(filename),
            self.fixtures[1].read(filename)
        ]

    def write(self, filename, data):
        for fixture in self.fixtures:
            fixture.write(filename, data)

    def write_signed(self, filename, data, signing_role):
        for fixture in self.fixtures:
            fixture.write_signed(filename, data, signing_role)
