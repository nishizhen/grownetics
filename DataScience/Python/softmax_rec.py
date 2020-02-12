import pandas as pd
import matplotlib.pyplot as plt
import numpy as np

# Rooms to look at
rooms = np.array([7,8,9,10])

# Median wet weight data across different rooms
median_ww = np.array([7,7.75,7,6.5])

inputData = pd.read_csv('/home/input.csv')

# grouped = inputData.groupby('source_id')
# for source in grouped:
#     # Print out each source_id we have in the data
#     print(source[0])
# print(grouped['source_id'].count())

'''softmax function. Takes a vector of data and produces a
probability mass function that favors higher values.
'''
def softmax(v):
    output = [np.exp(v[i])/np.sum(np.exp(v)) for i in range(len(v))]
    return np.array(output)

# Calculating weights based on median wet weight
weights = softmax(median_ww)

# Convert from pandas DataFrame to numpy array
tempf07 = np.array(inputData.loc[(inputData['source_id'] == 7) & (inputData['type'] == 3)]['sensor_data.mean_value'])
tempf08 = np.array(inputData.loc[(inputData['source_id'] == 8) & (inputData['type'] == 3)]['sensor_data.mean_value'])
tempf09 = np.array(inputData.loc[(inputData['source_id'] == 9) & (inputData['type'] == 3)]['sensor_data.mean_value'])
tempf10 = np.array(inputData.loc[(inputData['source_id'] == 10) & (inputData['type'] == 3)]['sensor_data.mean_value'])

humf07 = np.array(inputData.loc[(inputData['source_id'] == 7) & (inputData['type'] == 2)]['sensor_data.mean_value'])
humf08 = np.array(inputData.loc[(inputData['source_id'] == 8) & (inputData['type'] == 2)]['sensor_data.mean_value'])
humf09 = np.array(inputData.loc[(inputData['source_id'] == 9) & (inputData['type'] == 2)]['sensor_data.mean_value'])
humf10 = np.array(inputData.loc[(inputData['source_id'] == 10) & (inputData['type'] == 2)]['sensor_data.mean_value'])

co2f07 = np.array(inputData.loc[(inputData['source_id'] == 7) & (inputData['type'] == 4)]['sensor_data.mean_value'])
co2f08 = np.array(inputData.loc[(inputData['source_id'] == 8) & (inputData['type'] == 4)]['sensor_data.mean_value'])
co2f09 = np.array(inputData.loc[(inputData['source_id'] == 9) & (inputData['type'] == 4)]['sensor_data.mean_value'])
co2f10 = np.array(inputData.loc[(inputData['source_id'] == 10) & (inputData['type'] == 4)]['sensor_data.mean_value'])

# Rearrange into sample matrices
temp_samp = np.array([tempf07,tempf08,tempf09,tempf10])
hum_samp = np.array([humf07,humf08,humf09,humf10])
co2_samp = np.array([co2f07,co2f08,co2f09,co2f10])

# Expected value of samples given the softmax weights
# Multiply the weights by the sample matrices
temp_rec = np.dot(weights,temp_samp)
hum_rec = np.dot(weights,hum_samp)
co2_rec = np.dot(weights,co2_samp)

# Time vector (hours)
time = np.array([4*i for i in range(len(temp_rec))])

# Convert numpy arrays back to pandas DataFrames
ssh_rec_df = np.array([time, temp_rec, hum_rec, co2_rec]).transpose()
ssh_rec_frame = pd.DataFrame(ssh_rec_df,columns=['Time (Hours)', 'Temperature (Celsius)', 'Humidity','CO2 (PPM)'])

# Output csv of recommendations
ssh_rec_frame.to_csv(r'/home/output.csv', index=None, header=True)
