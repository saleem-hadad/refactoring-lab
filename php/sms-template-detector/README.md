# SmsTemplateDetector

The SMS template detector is a class that is responsible for parsing bank transactions SMS and extract information from each once including brand name, amount, ..etc.

## Get Started

1. Fork and clone the repo
2. Duplicate the v1 (the poorly written version)
3. Rename to your latest version (e.g. v2)
4. Refactor the code while making sure the tests in the "tests" directory still pass. You can add comments if needed to explain your thought process.
5. If the tests pass and you're satisfied with your changes, submit a pull request.

## To run the tests

```
./vendor/bin/pest
```

## What I did
- improved the test to cover more edge cases, e.g: using the other sms template
- extract arrays to the class; they may be used in another way from the class
- change redundant `str_replace` to `foreach` based on the rules in the `$templates` & `$patterns`
- simplify the comparison from `strpos(condition) !== false` to `strpos(condition)` directly
- simplify `! empty()` to `isset()`. positive conditions are easier and `empty()` has some weird behavior
- I have added types as much as I could. It makes the code easier to read.

I know there is more to refactor, but this is what I come to. I appreciate your review to learn from you :)