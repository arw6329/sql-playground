import argparse
import subprocess
import os

def main():
    parser = argparse.ArgumentParser(description='Run SQL worksheet application with specific databases enabled')
    parser.add_argument(
        '--postgres12', action='store_true', help='Enable PostgreSQL 12 database'
    )
    parser.add_argument(
        '--postgres13', action='store_true', help='Enable PostgreSQL 13 database'
    )
    parser.add_argument(
        '--postgres14', action='store_true', help='Enable PostgreSQL 14 database'
    )
    parser.add_argument(
        '--postgres15', action='store_true', help='Enable PostgreSQL 15 database'
    )
    parser.add_argument(
        '--postgres16', action='store_true', help='Enable PostgreSQL 16 database'
    )
    parser.add_argument(
        '--mysql8.0', action='store_true', help='Enable MySQL 8.0 database'
    )
    parser.add_argument(
        '--mysql8.4', action='store_true', help='Enable MySQL 8.4 database'
    )
    parser.add_argument(
        '--maria11.8.1', action='store_true', help='Enable MariaDB 11.8.1 database'
    )
    parser.add_argument(
        '--oracle23ai', action='store_true', help='Enable Oracle Database 23ai'
    )
    parser.add_argument(
        '--oracle21c', action='store_true', help='Enable Oracle Database 21c'
    )
    args = parser.parse_args()

    enabled_dbs = [ key for key, value in vars(args).items() if value ]
    profiles = [ x for key in enabled_dbs for x in ('--profile', key) ]

    subprocess.run(
        [ 'docker', 'compose', *profiles, 'up' ],
        stderr=subprocess.STDOUT,
        env={ **os.environ, 'ENABLED_DBS': ','.join(enabled_dbs) }
    )


if __name__ == '__main__':
    main()