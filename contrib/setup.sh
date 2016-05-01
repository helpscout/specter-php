#!/usr/bin/env bash
#
# Help Scout Contribution Tooling
# ==============================================================================
# Setup our pre-commit hooks if the tool is available
#
command -v pre-commit >/dev/null 2>&1 || {
    echo ""
    echo "We'd like for you to consider running our pre-commit hooks. To do so, you'll"
    echo "need to install the pre-commit tool from http://pre-commit.com/"
    echo "and then run 'pre-commit install' in this directory. Check out the"
    echo ".pre-commit-config.yaml file to learn more about our QA checks."
    exit 0;
}
echo ""
echo "Thank you for running pre-commit!. Installing the Help Scout Hooks"
echo "for this repo."
echo ""
pre-commit install
