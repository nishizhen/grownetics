FROM python
RUN pip install pandas matplotlib
# RUN mkdir /home/report
# RUN mkdir /home/data

COPY . /home
WORKDIR /home
CMD python /home/softmax_rec.py
