# ColoredLetterGenerator


CLI for producing graphics of specified letters in specified colors (and background colors)


## How to use

### Prerequisites

- PHP (with GD2)


### Execution

1. Start a command line and run `cli.php`.

2. Choose your configuration file. There are two files already provided in the `samples` directory.

3. Type an arbitrary name for the output directory.

Example:

```
$ php -f cli.php
Path to your configuration file: samples/uppercase-letters.json
Path to destination folder: sample_output
```

### Writing configuration files

It's best explained by an example:<br />
(Note: lines starting with `#` are comments. You have to remove them before running this application since JSON doesn't support any kind of comments.)

```
{   
    # Specify width and height of the resulting graphics
    "width": 100,
    "height": 100,

    # Specify the path to your font file
    "font": "Gauge-Regular.ttf",

    # You may optionally define this block
    # This program tries to automatically choose the highest font size which
    # fits into the bounding box specified above (width & height)
    # It therefore searches through a default range (1 .. 500) which can be modified by the following options:
    #
    # WARNING: Specifying illogical values (e.g. end > start or start > width/height) will eventually result in a crash!
    "fontSizeRange": {
      "start": 1,
      "end": 500
    }

    # The letters
    "letters": [
        "1",
        "2",
        "3",
        "4",
		"5",
        "6",
        "7",
        "8",
        "9",
        "0"
    ],

    # Background colors
    # You need to specifiy one color at least (e.g. white as in the sample below)
    "backgroundColors": [
        [
            255,
            255,
            255
        ]
    ],
 
    # The text colors you want to use (an array of an array of RGB values)
    "colors": [
        [
            # Three numbers which compose a RGB value 
            255,
            0,
            0
        ],
        [
            255,
            127,
            0
        ],
        [
            255,
            255,
            0
        ],
        [
            0,
            255,
            0
        ],
        [
            0,
            0,
            255
        ],
        [
            75,
            0,
            130
        ],
        [
            143,
            0,
            255
        ]
    ]
}
```

## Screenshot

![screenshot](http://i.stack.imgur.com/UY7sn.png)
