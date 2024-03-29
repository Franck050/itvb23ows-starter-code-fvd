FROM python:3.8
COPY . .
RUN pip install --no-cache-dir flask
CMD ["flask", "--app", "app" , "run", "--host", "0.0.0.0", "--debug"]
EXPOSE 5000