In this folder, you can put csv files with following format, to be able to use them in the formula like so

GRID("filename", [rows_field], [columns_field])

For example, if the file name is prices.csv, you can write
GRID("prices", [rows_field], [columns_field])

The format has to follow this structure

,0,100,200,300,400
0,1,2,3,4,5
100,6,7,8,9,10
200,11,12,13,14,15
300,16,17,18,19,20
400,21,22,23,24,25

Or if you want to label the rows/columns

width/height,0,100,200,300,400
0,1,2,3,4,5
100,6,7,8,9,10
200,11,12,13,14,15
300,16,17,18,19,20
400,21,22,23,24,25

Check the documentation section about the Grid feature to see how the module picks the corresponding value
from the two value that are provided
