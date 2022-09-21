@echo off
CD %1
ffmpeg -i audio.wav -acodec libmp3lame audio.mp3
ffmpeg -i audio.ogg -acodec libmp3lame audio.mp3
ffmpeg -i audio.ac3 -acodec libmp3lame audio.mp3
ffmpeg -i audio.aac -acodec libmp3lame audio.mp3
ffmpeg -i audio.wav -acodec ac3 audio.mp3

ffmpeg -i audio.wav  -acodec libfaac audio.aac
ffmpeg -i audio.ogg  -acodec libfaac audio.aac
ffmpeg -i audio.ac3  -acodec libfaac audio.aac

ffmpeg -i audio.wav  -acodec libvorbis audio.ogg
ffmpeg -i audio.ac3 -acodec libvorbis audio.ogg
ffmpeg -i audio.aac -libvorbis audio.ogg

ffmpeg -i audio.aac -acodec ac3 audio.ac3
ffmpeg -i audio.ogg -acodec ac3 audio.ac3

ffmpeg -i audio.ac3 audio.wav
ffmpeg -i audio.ogg audio.wav
ffmpeg -i audio.aac audio.wav
