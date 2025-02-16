# swiper-no-swiping
-- Tree Hacks 2025 Project --

Welcome to Swiper, No Swiping! This hardware and software annoyance and security system tracks the security of your belongings in real-time. Need to leave your backpack, bike, or other important belonging unattended in a public place? Simply insert or affix the Swiper, No Swiping device and you're all set!

## What it does

The SNS device constantly monitors for movement. If it detects that it moved, ie someone has picked up your important belonging, it will buzz loudly and uncontrollably, frightening the culprit until they leave your bag alone. Not only that, but it will send you an email notification so you can come back to check on your belongings. What's more, you can access the web server to see the history of your device's movement. If it only moved for a brief few seconds, perhaps it was merely pushed - but if the convenient graph indicates that it moved for an extended period of time, you know someone is trying to steal your stuff.

## How it's built

How does it work? The Swiper, No Swiping device operates off of an ESP8266 microcontroller, MPU6050 gyroscope, and buzzer. When the gyroscope detects excessive movement, it will sound the buzzer and send an HTTP POST request via WiFi to the web server. The web server will receive the movement log and post it onto a database while also sending the email notification to the user (such long as it hasn't been less than 10 seconds since the last notification - we want to keep the user informed of all movements, but also not spam them too much). Finally, when any user accesses the web application, they will see the movement history of the device accessed via the database. When not in use, the SNS device can simply be powered off.

## Inspiration

This project was inspired by the fright of leaving your expensive personal belongings at a hackathon - or airport, or train station, or anywhere for that matter. Want to leave your bag to claim a seat or desk but don't want it stolen? Simply place the SNS device in your backpack and wander off in peace, knowing that the moment anyone tries to move your bag, you will instantly be notified and the fright of the sound will scare the culprit off.

## Challenges, accomplishments, learning experience

Integrating hardware-software communication, dealing with broken ESP32 boards!

## What's next for Swiper, No Swiping!

What I had wanted to implement this hackathon, but couldn't due to broken ESP32 boards, was connectivity to Bluetooth speakers. Users would be able to record a custom scare-off recording on the web app which would upload to the device. Then, instead of buzzing, the device will play this recording to scare the thieves away. Additionally, the ability to enable and disable the device through the web app can be implemeneted.
