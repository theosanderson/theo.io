---

title: "Jaxmetal troubleshooting tips"

subtitle: ""

summary: ""

authors: [admin]

categories: []

date: 2023-07-28


featured: false

draft: false

projects: []



---
*This post serves only to help people Googling for the problems I encountered here*

I recently installed JAX-metal which allows JAX to use MacOS GPUs. I aimed to follow the instructions [here](https://developer.apple.com/metal/jax/)

However I hit some issues, when running `python build/build.py --bazel_options=--@xla//xla/python:enable_tpu=true`. Initially I got an error about not having accepted the Xcode agreement, which prompted me to accept it with another terminal command. But running that terminal command gave me

```
xcode-select: error: tool 'xcodebuild' requires Xcode, but active developer directory '/Library/Developer/CommandLineTools' is a command line tools instance
```


My Xcode was pointing not to real Xcode just to the command line tools, so I had to fix that with:
```
sudo xcode-select -s /Applications/Xcode.app/Contents/Developer
```

Next I got a message that SDK 10.10 could not be found. I listed SDKS with `xcodebuild -showsdks` and found I had 13.3.

I ran `nano .bazelrc` and added a line reading `build --macos_sdk_version=13.3`.

At this point compilation worked.