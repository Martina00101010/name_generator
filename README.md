# name_generator

This is a simple program to generate random names. It learnes the patterns of letter position and uses the found frequence to generate new names.

Program already has some trained markov chains in files started with "serialized". However, if you want to train it differently, you should change files "female_names" or "male_names". File must contain only names: one name per line.

To train chain yourself type
```
php main.php
```

To generate names type also "f" for female names or "m" for male ones. Also specify the number to generate. Like this:
```
php main.php f 5
```
