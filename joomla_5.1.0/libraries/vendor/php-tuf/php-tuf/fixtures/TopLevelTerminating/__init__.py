# Delegation tree
#
#             Targets
#             /     \
#            a       b
#
# a is the only terminating delegation
#
# Roles should be evaluated in the order:
# Targets > a
#
# Role b should not be evaluated.
from fixtures.builder import ConsistencyVariantFixtureBuilder


def build():
    ConsistencyVariantFixtureBuilder('TopLevelTerminating')\
        .publish(with_client=True)\
        .create_target('targets.txt')\
        .delegate('a', ['*.txt'], terminating=True)\
        .create_target('a.txt', signing_role='a')\
        .delegate('b', ['*.txt'])\
        .create_target('b.txt', signing_role='b')\
        .publish()
