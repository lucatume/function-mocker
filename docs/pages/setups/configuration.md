---
title: Configuration
url: /setups/configuration.html
permalink: /setups/configuration.html
sidebar_link: true
---

## Introduction 
To configure Function Mocker to work in specific cases and in combination with different testing framework see the [Different setups section](/different-setups.html).

## Testing environments
Function Mocker will work whether WordPress is loaded in the same variable scope as the test or not.  
But when Function Mocker is used without loading WordPress, and any other theme and plugin with it, some utility functions providing basic functionalities, might be missing.  
As an example I might be unit-testing code that calls the `add_action` and `do_action` functions; those functions are not internal PHP functions