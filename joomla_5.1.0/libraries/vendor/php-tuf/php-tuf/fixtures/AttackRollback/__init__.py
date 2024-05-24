from fixtures.builder import ConsistencyVariantFixtureBuilder

import shutil


def build():
    builder = ConsistencyVariantFixtureBuilder('AttackRollback')\
        .create_target('testtarget.txt')\
        .publish(with_client=True)

    for fixture in builder.fixtures:
        server_dir = fixture._server_dir
        backup_dir = server_dir + '_backup'
        shutil.copytree(server_dir, backup_dir, dirs_exist_ok=True)

        fixture.create_target('testtarget2.txt')\
            .publish(with_client=True)
        shutil.rmtree(server_dir + '/')

        # Reset the client to previous state to simulate a rollback attack.
        shutil.copytree(backup_dir, server_dir, dirs_exist_ok=True)
        shutil.rmtree(backup_dir + '/')
