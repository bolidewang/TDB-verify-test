#coding:utf-8
import sys,os
from PIL import Image,ImageDraw

#二值数组
# G: Integer 图像二值化阀值 
t2val = {}
def twoValue(image,G):
    for y in xrange(0,image.size[1]):
        for x in xrange(0,image.size[0]):
            g = image.getpixel((x,y))
            if g > G:
                t2val[(x,y)] = 1
            else:
                t2val[(x,y)] = 0


def saveImage(filename,size):
    image = Image.new("1",size)
    draw = ImageDraw.Draw(image)

    for x in xrange(0,size[0]):
        for y in xrange(0,size[1]):
            draw.point((x,y),t2val[(x,y)])

    image.save(filename)

#count = 0;
#def saveTwoValueToData():
#    for x in xrange(0,size[0]):
#        for y in xrange(0,size[1]):
#            g = image.getpixel((x,y))
#            for i in xrange(0,3):
#                count = (count + 1)%16
#                if (count  == 0):
#                    print "0x%02x,/n"%(pixel[i])
#                else:
#                    print "0x%02x,"%(pixel[i])

image_File_Name = raw_input("type the image file name: ")
image = Image.open("/Users/bolide/Documents/栽植烟酒僧/多媒体技术/" + image_File_Name + ".png").convert("L")
twoValue(image,125)
saveImage("/Users/bolide/Documents/栽植烟酒僧/多媒体技术/" + image_File_Name + "-2.png",image.size)

