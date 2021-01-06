#!/usr/bin/env python3

import os
import sys
import shutil
from os.path import join

# do some clean up works and prepare the build directory. 
def clean(build_path: str) -> None:
    if os.path.exists(build_path) :
        print("Target path `build/` is already exists. Try to remove it...")
        shutil.rmtree(build_path)
        print("Removed and re-create a new one...")
    else:
        print("Create a target path `build/` ...")
    os.mkdir(build_path)
    return    

# copy php files.
def copy_file(src_path: str, build_path: str) -> None:
    print("Copy plugin files...")
    shutil.copy(join(src_path, 'wordpress-ultimate-toolkit.php'), build_path)
    shutil.copytree(join(src_path, 'inc'), join(build_path, 'inc'))
    shutil.copytree(join(src_path, 'widgets'), join(build_path, 'widgets'))
    print("Done.")
    return
    
if __name__ == '__main__':
    src_path = os.path.dirname(os.path.abspath(sys.argv[0]))
    build_path = os.path.join(src_path, 'build')
    
    clean(build_path)
    copy_file(src_path, build_path)
    
    sys.exit(0)
